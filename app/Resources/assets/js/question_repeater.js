/**
 * This object organizes the javascript used by this twig.
 * It handles all the client side functionality required to make use of embedded form collections,
 * and the ability to add and remove objects, such as adding new questions and alternatives to a interview schema.
 * It uses prototypes, which are blueprints with placeholder values, to dynamically generate new form components.
 *
 * See: http://symfony.com/doc/current/cookbook/form/form_collections.html
 *
 * The onReady function is called when the document is ready.
 */
const QuestionManager = {

  // The container for all the questions
  questions: $('#questions'),

  // The add question button
  addQuestionButton: $('#addQuestionButton'),

  // The prototype for a question alternative
  aPrototype: $('#prototypes').data('prototype-a'),

  // The prototype for a question
  qPrototype: $('#prototypes').data('prototype-q'),

  onReady: function () {
    // Set up the questions that are already in the database (add buttons etc)
    QuestionManager.questions.find('.card').each(QuestionManager.setUp);

    // Find the initial number of questions in the schema
    QuestionManager.questions.data('q-index', QuestionManager.questions.length);

    // Bind the add question functionality to the add question button
    QuestionManager.addQuestionButton.on('click', QuestionManager.addQuestion);
  },

  setUp: function (i) {
    var alternativeList = $(this).find('.alternatives');
    var alternatives = alternativeList.find('.form-group');
    var type = $(this).find('select');

    alternativeList.data('a-index', alternatives.length);
    alternativeList.data('q-index', i);

    QuestionManager.addQuestionDeleteButton($(this));

    var alternativeButton = QuestionManager.addAlternativeAddButton($(this));

    QuestionManager.toggleAlternatives(type, alternativeList, alternativeButton);

    type.on('change', function () {
      QuestionManager.toggleAlternatives(type, alternativeList, alternativeButton);
    });

    alternatives.each(function () {
      QuestionManager.addAlternativeDeleteButton($(this));
    });
  },

  addQuestion: function (event) {
    event.preventDefault();

    var qIndex = QuestionManager.questions.data('q-index');
    var newQuestion = $(QuestionManager.qPrototype.replace(/__q_prot__/g, qIndex));
    var newAlternatives = $('<div></div>').addClass('alternatives col-12 col-md-8 col-lg-7 col-xl-6');
    var newButtons = $('<div></div>').addClass('buttons col-8 col-md-6 col-lg-5');

    newAlternatives.data('a-index', 0);
    newAlternatives.data('q-index', qIndex);

    var question = $('<div></div>').addClass('card question-card');
    var questionHeader = $('<div>Spørsmål</div>').addClass('card-header');

    var cardBody = $('<div></div>').addClass('card-body')
      .append(newQuestion)
      .append($('<div></div>').addClass('row').append(newAlternatives))
      .append($('<div></div>').addClass('row').append(newButtons));

    question.append(questionHeader).append(cardBody);

    var questionCard = cardBody.parent();

    QuestionManager.addQuestionDeleteButton(questionCard);
    var alternativeButton = QuestionManager.addAlternativeAddButton(cardBody);
    alternativeButton.hide();
    QuestionManager.addOrderButtons(questionCard);

    QuestionManager.questions.append(question);

    var type = newQuestion.find('select');

    type.on('change', function () {
      QuestionManager.toggleAlternatives(type, newAlternatives, alternativeButton);
    });

    QuestionManager.questions.data('q-index', qIndex + 1);
  },

  addQuestionDeleteButton: function (questionCard) {
    var deleteButton = $('<button class="float-right clickable btn btn-sm btn-outline-danger">Slett&nbsp;<i class="fa fa-trash-o text-danger"></i></button>');
    var header = questionCard.find('.card-header').first();

    header.append(deleteButton);

    deleteButton.on('click', function (event) {
      event.preventDefault();
      questionCard.remove();
    });
  },

  addAlternativeAddButton: function (panel) {
    var alternativeButton = $('<a href="#" class="button tiny"><i class="fa fa-plus"></i> Alternativ</a>');
    var alternatives = panel.find('.alternatives');
    var buttons = panel.find('.buttons');

    buttons.append(alternativeButton);

    alternativeButton.on('click', null, alternatives, QuestionManager.addAlternative);

    return alternativeButton;
  },

  addAlternativeDeleteButton: function (alternative) {
    var deleteButton = $('<button class="btn btn-sm btn-outline-danger float-right">Slett&nbsp;<i class="fa fa-trash-o"></i></button>');

    alternative.append(deleteButton);

    deleteButton.on('click', function (event) {
      event.preventDefault();
      alternative.closest('.alternative').remove();
    });
  },

  addAlternative: function (event) {
    event.preventDefault();
    var alternatives = event.data;

    var aIndex = alternatives.data('a-index');
    var qIndex = alternatives.data('q-index');

    var upButton = $('<button class="btn btn-sm btn-outline-secondary">Opp&nbsp;<i class="fa fa-arrow-up"/></button>');
    var downButton = $('<button class="btn btn-sm btn-outline-secondary">Ned&nbsp;<i class="fa fa-arrow-down"/></button>');

    upButton.on('click', function (e) {
      e.preventDefault();
      upButton.closest('.alternative').insertBefore(upButton.closest('.alternative').prev('.alternative'));
    });
    downButton.on('click', function (e) {
      e.preventDefault();
      downButton.closest('.alternative').insertAfter(downButton.closest('.alternative').next('.alternative'));
    });

    var newAlternative = $(QuestionManager.aPrototype.replace(/__a_prot__/g, aIndex).replace(/__q_prot__/g, qIndex));

    var alternativeRow = $('<div class="alternative"></div>');
    alternativeRow.append(newAlternative).append(upButton).append(downButton);

    newAlternative.append(upButton);
    newAlternative.append(downButton);
    alternatives.data('a-index', aIndex + 1);
    alternatives.append(alternativeRow);

    QuestionManager.addAlternativeDeleteButton(newAlternative);
  },

  toggleAlternatives: function (select, alternatives, alternativeButton) {
    console.log(select.val())
    if (select.val() == 'text') {
      alternatives.empty();
      alternativeButton.hide();
    } else if (select.val() == 'radio') {
      alternativeButton.show();
    } else {
      alternativeButton.show();
    }
  },

  addOrderButtons: function (questionCard) {
    var upButton = $('<button class="btn btn-sm btn-outline-secondary float-right">Opp&nbsp;<i class="fa fa-arrow-up"/></button>');
    var downButton = $('<button class="btn btn-sm btn-outline-secondary mx-2 float-right">Ned&nbsp;<i class="fa fa-arrow-down"/></button>');
    var header = questionCard.find('.card-header').first();

    upButton.on('click', function (e) {
      e.preventDefault();
      questionCard.insertBefore(questionCard.prev('.question-card'));
    });
    downButton.on('click', function (e) {
      e.preventDefault();
      questionCard.insertAfter(questionCard.next('.question-card'));
    });

    header.append(downButton);
    header.append(upButton);

    return upButton;
  },
};
