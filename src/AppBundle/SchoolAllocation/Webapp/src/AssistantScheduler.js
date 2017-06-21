const weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

export class AssistantScheduler {

  constructor(schools, assistants) {
    this.schedule = new Schedule(schools, assistants);
  }

  scheduleGreedily() {
    const schedule = this.schedule;
    const assistants = deepCopy(schedule.queuedAssistants);

    for (let i = 0; i < assistants.length; i++) {
      const assistant = assistants[i];
      const double = assistant.double;

      if (double) {
        for (let i = 0; i < weekDays.length; i++) {
          const day = weekDays[i];
          if (assistant[day] && schedule.hasCapacity(day, 1) && schedule.hasCapacity(day, 2)) {
            schedule.assignAssistantTo(assistant, day, 1);
            schedule.assignAssistantTo(assistant, day, 2);
            break;
          }
        }
      } else if (assistant.preferredGroup === 1) {
        for (let i = 0; i < weekDays.length; i++) {
          const day = weekDays[i];
          if (assistant[day] && schedule.hasCapacity(day, 1)) {
            schedule.assignAssistantTo(assistant, day, 1);
            break;
          }
        }
      } else if (assistant.preferredGroup === 2) {
        for (let i = 0; i < weekDays.length; i++) {
          const day = weekDays[i];
          if (assistant[day] && schedule.hasCapacity(day, 2)) {
            schedule.assignAssistantTo(assistant, day, 2);
            break;
          }
        }
      } else {
        for (let i = 0; i < weekDays.length; i++) {
          const day = weekDays[i];
          if (assistant[day] && schedule.hasCapacity(day, 1)) {
            schedule.assignAssistantTo(assistant, day, 1);
            break;
          } else if (assistant[day] && schedule.hasCapacity(day, 2)) {
            schedule.assignAssistantTo(assistant, day, 2);
            break;
          }
        }
      }
    }

    return schedule;
  }
}

export class Schedule {
  constructor(schools, assistants) {
    this.queuedAssistants = deepCopy(assistants);

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
    return JSON.parse(JSON.stringify(this));
  }
}

export function deepCopy(obj) {
  return JSON.parse(JSON.stringify(obj));
}
