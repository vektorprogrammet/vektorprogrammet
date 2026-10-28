# Existing interview schemas: question indexing and ordering

**Status: investigated, not fixed.** This report describes confirmed code defects
and their likely effect on editing existing interview schemas. It does not claim
that every possible edit failure has the same cause. In particular, a failure to
open the page or save a title alone still needs its own request/exception evidence.

## Affected flow

- List: `/kontrollpanel/intervju/skjema`.
- Edit: `/kontrollpanel/intervju/skjema/{id}`.
- Controller: `InterviewSchemaController::editSchemaAction()` creates an
  `InterviewSchemaType`, handles the submitted collection, persists it and flushes.
- The shared question template renders existing questions and prototypes;
  `question_repeater.js` creates/removes question and alternative fields in the browser.

## Confirmed defect: new questions reuse an existing collection index

In `app/Resources/assets/js/question_repeater.js`, `onReady()` initializes:

```javascript
QuestionManager.questions.data('q-index', QuestionManager.questions.length);
```

`QuestionManager.questions` is `$('#questions')`: the collection **container**,
not its question cards. Its length is normally **1**, even when the form already
contains many questions. `addQuestion()` then uses that value to replace
`__q_prot__` in the Symfony field prototype and increments it afterward.

For a schema with existing question indexes `0`, `1`, `2`, the first appended
question therefore receives a name such as:

```text
interviewSchema[interviewQuestions][1][question]
```

That duplicates the existing question at index `1`. Both values can appear in
the browser, but PHP/Symfony receive them under the same collection key rather
than as two independently identified questions. Values can be overwritten or
mapped to the wrong entry; the UI can appear to accept an edit without preserving
the intended set of questions. Other errors may also follow from the malformed
submitted data. A brand-new form is less likely to expose the collision because
there are no existing entries to collide with.

The index initialization was reproduced by executing the checked-in `onReady()`
in Node with a small jQuery stub representing one container. It produced index
`1`, conflicting with the pre-existing indexes above. No browser-backed save or
production database mutation was performed during this investigation.

## Reproduce locally without changing production

1. Follow the [local setup guide](../local-development.md), load fixtures and log
   in as `admin` or `teamleader`.
2. Open `/kontrollpanel/intervju/skjema/2`. The fixture
   `Intervjuskjema NTNU, 2015` has eight existing questions.
3. In the browser console, inspect `$('#questions').data('q-index')`: it is `1`
   rather than the next unused question key.
4. Click **Nytt Spørsmål**, then inspect the question input names:

   ```javascript
   Array.from(document.querySelectorAll('#questions input[name$="[question]"]'))
     .map(input => input.name)
   ```

5. Observe the repeated index `1` in the existing and newly added input names.
   Inspect the submitted form payload on a disposable local schema if testing
   save behavior. Do not use a live recruitment schema for this experiment.

## Related confirmed ordering limitations

- Existing cards are initialized by `setUp()`, which adds delete/alternative
  controls but does **not** call `addOrderButtons()`. Newly created cards do call
  it, so existing and new questions have different ordering controls.
- Order buttons move DOM cards but do not renumber submitted field names or
  create an explicit persisted position.
- `InterviewSchema::interviewQuestions` is a ManyToMany association without an
  explicit position column or `OrderBy` declaration. A stable saved order is not
  represented by a dedicated field. Moving elements in the DOM alone does not
  establish a reliable database ordering contract.

These observations require separate acceptance criteria from the duplicate-key
bug. A future fix should verify save/reload behavior, not only visual movement.

## Why existing tests do not catch it

`tests/AppBundle/Controller/InterviewControllerTest.php::testEditSchemas()` opens
schema 1, changes its **name**, submits it and checks the list. It does not add,
remove or reorder questions. Symfony BrowserKit does not execute this JavaScript.
Passing that test therefore does not validate editing the question collection.

## Follow-up scope (no implementation in this PR)

- Choose unused question/alternative keys from actual rendered field names,
  including after deletion or a failed submission with sparse keys.
- Give existing and new entries consistent controls.
- Define how order is stored and reloaded, including alternatives if required.
- Verify the collection's add/remove persistence behavior and its impact on
  questions shared by multiple schemas or referenced by historical interviews.
- Cover existing-schema append/remove/order/save/reload in a real browser test,
  with distinct question text so overwritten or reordered values are detectable.

## Source map

- [QuestionManager](../../app/Resources/assets/js/question_repeater.js)
- [Existing fields and prototypes](../../app/Resources/views/questions/repeatable_question.html.twig)
- [Schema editor template](../../app/Resources/views/interview/schema.html.twig)
- [Controller](../../src/AppBundle/Controller/InterviewSchemaController.php)
- [Schema form](../../src/AppBundle/Form/Type/InterviewSchemaType.php)
- [Schema entity](../../src/AppBundle/Entity/InterviewSchema.php)
- [Current controller tests](../../tests/AppBundle/Controller/InterviewControllerTest.php)
