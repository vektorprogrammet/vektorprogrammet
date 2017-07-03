function randomElement(arr) {
  const index = Math.floor(Math.random()*arr.length);

  return arr[index];
}

const weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

export class Schedule {
  constructor(schools, assistants) {
    this.queuedAssistants = assistants;
    this.schools = schools;
    weekDays.forEach(day => {
      this[day] = 0;
      this[day + "AssistantsGroup1"] = [];
      this[day + "AssistantsGroup2"] = [];
    })

    this._updateCapacitiesFromSchools(schools);
  }

  _updateCapacitiesFromSchools(schools) {
    for (let i = 0; i < schools.length; i++) {
      const school = schools[i];
      weekDays.forEach(day => {
        this[day] += school[day];
      })
    }
  }

  hasCapacity(day, group) {
    const assistantsKey = day + "AssistantsGroup" + group;

    if (!this.hasOwnProperty(day)) return false;
    if (!this.hasOwnProperty(assistantsKey)) return false;

    return this[day] > this[assistantsKey].length;
  }

  assignAssistantTo(assistant, day, group) {
    const assistantsKey = day + "AssistantsGroup" + group;

    if (!this.hasOwnProperty(day)) return false;
    if (!this.hasOwnProperty(assistantsKey)) return false;

    this[assistantsKey].push(assistant);
    this._removeAssistantFromList(assistant, this.queuedAssistants);
  }

  _removeAssistantFromList(assistant, list) {
    const index = list.indexOf(list.find(a => a.id === assistant.id));
    if (index > -1) {
      list.splice(index, 1);
    }
  }

  reassignAssistant(assistant, fromDay, toDay, fromGroup, toGroup) {
    const assistantsKeyFrom = fromDay + "AssistantsGroup" + fromGroup;
    const assistantsKeyTo = toDay + "AssistantsGroup" + toGroup;
    //console.log("Reassigning ", assistant.name, fromDay, toDay);
    this[assistantsKeyTo].push(assistant);
    this._removeAssistantFromList(assistant, this[assistantsKeyFrom]);
  }

  schoolsHaveMoreCapacity() {
    weekDays.forEach(day => {
      if (this[day] > 0) {
        return true;
      }
    });

    return false;
  }

  deepCopy() {
    const clone = new Schedule(this.schools, this._copy(this.queuedAssistants));
    for (let i = 0; i < weekDays.length; i++){
      const day = weekDays[i];
      clone[day + "AssistantsGroup1"] = this._copy(this[day + "AssistantsGroup1"]);
      clone[day + "AssistantsGroup2"] = this._copy(this[day + "AssistantsGroup2"]);
    }
    return clone;
  }

  _copy(a) {
    return a.slice();
  }

  fitness() {
    let fitness = 0;
    for (let i = 0; i < weekDays.length; i++){
      const day = weekDays[i];
      fitness += this[day + "AssistantsGroup1"].length;
      fitness += this[day + "AssistantsGroup2"].length;
    }
    return fitness;
    //return -this.queuedAssistants.length;
  }

  mutate() {
    const clone = this.deepCopy();
    const group = randomElement([1, 2]);
    const fromDay = randomElement(weekDays);
    const assistantsKey = fromDay + "AssistantsGroup" + group;
    const assistants = this[assistantsKey];

    const length = assistants.length;
    if (length === 0) {
      return clone;
    }
    const assistant = randomElement(assistants);
    const availableDays = weekDays.filter(weekday => assistant[weekday] && fromDay !== weekday);
    if (availableDays.length === 0) {
      return clone;
    }
    const double = assistant.double;
    const toDay = randomElement(availableDays);
    if (double) {
      if (this.hasCapacity(toDay, 1) && (this.hasCapacity(toDay, 2))) {
        clone.reassignAssistant(assistant, fromDay, toDay, 1, 1);
        clone.reassignAssistant(assistant, fromDay, toDay, 2, 2);
        return clone;
      }
    } else if (assistant.preferredGroup === group){
      if (this.hasCapacity(toDay, group)) {
        clone.reassignAssistant(assistant, fromDay, toDay, group, group);
        return clone;
      }
    } else {
      const randomGroup = randomElement([1, 2]);
      if (this.hasCapacity(toDay, randomGroup)) {
        clone.reassignAssistant(assistant, fromDay, toDay, group, randomGroup);
        return clone;
      }
    }

    return clone;
  }

}
