const assert = require('assert');
const fs = require('fs');
const vm = require('vm');

let cards = ['0', '3', '8'].map(key => ({ key }));
const data = {};
let order = '';
const field = { val(value) { if (arguments.length) order = value; return order; } };
const form = { find() { return field; }, on() {} };
const collection = {
  each(fn) { cards.slice().forEach(card => fn.call(card)); return this; }
};
const questions = {
  find(selector) { assert.strictEqual(selector, '.question-card'); return collection; },
  children() { return collection; },
  closest() { return form; },
  append(card) { cards = cards.filter(item => item !== card).concat(card); },
  data(key, value) { if (arguments.length === 2) data[key] = value; return data[key]; }
};
function $(value) {
  if (value === '#questions') return questions;
  if (value === '#addQuestionButton') return { on() {} };
  if (value === '#prototypes') return { data() { return ''; } };
  return { attr() { return value.key; } };
}
const context = vm.createContext({ $, console });
vm.runInContext(fs.readFileSync('app/Resources/assets/js/question_repeater.js', 'utf8') +
  '\nthis.manager = QuestionManager;', context);
const manager = context.manager;
manager.setUp = function () {};
manager.onReady();
assert.strictEqual(data['q-index'], 9);
assert.strictEqual(manager.nextIndex({ each() {} }), 0);
const alternatives = { each(fn) { ['0', '4'].forEach(key => fn.call({ key })); } };
assert.strictEqual(manager.nextIndex(alternatives), 5);

// Reordering must preserve sparse keys and serialize visual order, not numeric order.
cards = [cards[2], cards[0], cards[1]];
manager.writeOrder();
assert.deepStrictEqual(JSON.parse(order), ['8', '0', '3']);

// A failed validation must restore the submitted order when the form is rendered again.
cards.sort((a, b) => Number(a.key) - Number(b.key));
manager.restoreOrder();
assert.deepStrictEqual(cards.map(card => card.key), ['8', '0', '3']);
cards.splice(1, 1);
cards.push({ key: '9' });
manager.writeOrder();
assert.deepStrictEqual(JSON.parse(order), ['8', '3', '9']);
order = 'not json';
assert.doesNotThrow(() => manager.restoreOrder());
cards = [];
manager.writeOrder();
assert.strictEqual(order, '[]');
console.log('Collection key and question order checks passed.');
