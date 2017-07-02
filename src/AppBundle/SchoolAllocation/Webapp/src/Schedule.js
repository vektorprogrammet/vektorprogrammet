const weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

export class Schedule {
  constructor(schools, assistants) {
    this.queuedAssistants = assistants;
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
    this._removeAssistantFromQueue(assistant);
  }

  _removeAssistantFromQueue(assistant) {
    const index = this.queuedAssistants.indexOf(this.queuedAssistants.find(a => a.id === assistant.id));
    if (index > -1) {
      this.queuedAssistants.splice(index, 1);
    }
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
    const schedule = Object.assign(Object.create(Object.getPrototypeOf(this)), this);
    console.log(schedule);

    return schedule;
  }
}
