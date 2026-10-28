const assert = require('assert');
const fs = require('fs');
const vm = require('vm');

const keys = ['0', '3', '8'];
const data = {};
const cards = {
  each(fn) { keys.forEach(key => fn.call({ key })); return this; }
};
const questions = {
  find(selector) { assert.strictEqual(selector, '.question-card'); return cards; },
  data(key, value) { if (arguments.length === 2) data[key] = value; return data[key]; }
};
const button = { on() {} };
function $(value) {
  if (value === '#questions') return questions;
  if (value === '#addQuestionButton') return button;
  if (value === '#prototypes') return { data() { return ''; } };
  return { attr() { return value.key; } };
}
const context = vm.createContext({ $, console });
const script = fs.readFileSync('app/Resources/assets/js/question_repeater.js', 'utf8');
vm.runInContext(script + '\nthis.manager = QuestionManager;', context);
context.manager.setUp = function () {};
context.manager.onReady();
assert.strictEqual(data['q-index'], 9, 'next question key must exceed all existing keys');
assert.strictEqual(context.manager.nextIndex({ each() {} }), 0, 'empty collection starts at zero');
const alternatives = { each(fn) { ['0', '4'].forEach(key => fn.call({ key })); } };
assert.strictEqual(context.manager.nextIndex(alternatives), 5, 'sparse alternative keys stay unique');
console.log('Question and alternative collection index checks passed.');
