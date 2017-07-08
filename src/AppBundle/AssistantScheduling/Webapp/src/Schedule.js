function randomElement(arr) {
  const index = Math.floor(Math.random() * arr.length);

  return arr[index];
}

const weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

let busyAssistants = [];

export class Schedule {
  constructor(schools, assistants) {
    this.queuedAssistants = this._copy(assistants);
    this.schools = schools;
    weekDays.forEach(day => {
      this[day] = 0;
      this[day + "AssistantsGroup1"] = [];
      this[day + "AssistantsGroup2"] = [];
    });

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

  getAssignedAssistants(day, group) {
    const assistantsKey = day + "AssistantsGroup" + group;
    return this[assistantsKey];
  }

  swapFromQueue(assistantInQueue, assignedAssistant, day, group, double) {
    if (double) {
      this.assignAssistantTo(assistantInQueue, day, 1);
      this.assignAssistantTo(assistantInQueue, day, 2);
    } else {
      this.assignAssistantTo(assistantInQueue, day, group);
    }

    this._removeAssistantFromList(assignedAssistant, this[day + "AssistantsGroup" + 1]);
    this._removeAssistantFromList(assignedAssistant, this[day + "AssistantsGroup" + 2]);
    this.queuedAssistants.push(assignedAssistant);
  }

  assignAssistantTo(assistant, day, group) {
    const assistantsKey = day + "AssistantsGroup" + group;

    if (!this.hasOwnProperty(day)) return false;
    if (!this.hasOwnProperty(assistantsKey)) return false;

    this[assistantsKey].push(assistant);
    this._removeAssistantFromList(assistant, this.queuedAssistants);

    busyAssistants.push(assistant);
    assistant.assignedDay = assistantsKey;
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
    this[assistantsKeyTo].push(assistant);
    this._removeAssistantFromList(assistant, this[assistantsKeyFrom]);
    assistant.assignedDay = assistantsKeyTo;
  }

  deepCopy() {
    const clone = new Schedule(this.schools, this._copy(this.queuedAssistants));
    for (let i = 0; i < weekDays.length; i++) {
      const day = weekDays[i];
      clone[day + "AssistantsGroup1"] = this._copy(this[day + "AssistantsGroup1"]);
      clone[day + "AssistantsGroup2"] = this._copy(this[day + "AssistantsGroup2"]);
    }
    return clone;
  }

  _copy(a) {
    return a.slice();
  }

  isOptimal() {
    const schoolsFull = this.totalCapacity() === this.scheduledAssistantsCount();
    const allAssistantsScheduled = this.queuedAssistants.length === 0;

    return schoolsFull || allAssistantsScheduled;
  }

  totalCapacity() {
    return weekDays.reduce((acc, day) => (
        acc + this[day] * 2 // Group 1 & group 2
    ), 0);
  }

  scheduledAssistantsCount() {
    let count = 0;
    for (let i = 0; i < weekDays.length; i++) {
      const day = weekDays[i];
      count += this[day + "AssistantsGroup1"].length;
      count += this[day + "AssistantsGroup2"].length;
    }
    return count;
  }

  fitness() {
    return this.scheduledAssistantsCount();
  }

  removeAssistant(assistant) {
    this._removeAssistantFromList(assistant, this.queuedAssistants);
    for (let group = 1; group <= 2; group++) {
      for (let i = 0; i < weekDays.length; i++) {
        const day = weekDays[i];
        this._removeAssistantFromList(assistant, this[day + "AssistantsGroup" + group]);
      }
    }
  }

  isValid() {
    let valid = true;

    if (this.scheduledAssistantsCount() > this.totalCapacity()) {
      console.error(this.scheduledAssistantsCount() + " assistants were scheduled, but the capacity is only " + this.totalCapacity());
      valid = false;
    }

    for (let group = 1; group <= 2; group++) {
      for (let i = 0; i < weekDays.length; i++) {
        const day = weekDays[i];
        const assistants = this.getAssignedAssistants(day, group);

        for (let j = 0; j < assistants.length; j++) {
          const assistant = assistants[j];

          const inNonPreferredGroup = assistant.preferredGroup !== null && assistant.preferredGroup !== group;
          if (inNonPreferredGroup) {
            console.error("Assistant prefers group " + assistant.preferredGroup + ", but was assigned to group " + group);
          }

          const assignedToMultipleDays = !this._assistantAssignedToOnlyOneDay(assistant);
          if (assignedToMultipleDays) {
            console.error("Assistant assigned to multiple days");
          }

          const overBooked = !assistant.double && this._assistantHasDoublePosition(assistant, day);
          if (overBooked) {
            console.error("Assistant doesn't want double position but has been assigned to both groups");
          }

          if (inNonPreferredGroup || assignedToMultipleDays || overBooked) {
            console.error(assistant);
            console.error("-----------------------------------------");
            valid = false;
          }
        }

      }
    }

    return valid;
  }

  _assistantAssignedToOnlyOneDay(assistant) {
    const assignedDays = [];

    for (let group = 1; group <= 2; group++) {
      for (let i = 0; i < weekDays.length; i++) {
        const day = weekDays[i];
        const assistants = this.getAssignedAssistants(day, group);

        if (assistants.indexOf(assistant) !== -1) {
          if (assignedDays.indexOf(day) === -1) {
            assignedDays.push(day);
          }
        }

      }
    }

    return assignedDays.length === 1;
  }

  _assistantHasDoublePosition(assistant, day) {
    const assistantsInGroup1 = this.getAssignedAssistants(day, 1);
    const assistantsInGroup2 = this.getAssignedAssistants(day, 2);

    return assistantsInGroup1.indexOf(assistant) !== -1 && assistantsInGroup2.indexOf(assistant) !== -1;
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
    } else if (assistant.preferredGroup === group) {
      if (this.hasCapacity(toDay, group)) {
        clone.reassignAssistant(assistant, fromDay, toDay, group, group);
        return clone;
      }
    } else if (assistant.preferredGroup === null) {
      const randomGroup = randomElement([1, 2]);
      if (this.hasCapacity(toDay, randomGroup)) {
        clone.reassignAssistant(assistant, fromDay, toDay, group, randomGroup);
        return clone;
      }
    }

    return clone;
  }

  mutate2() {
    const clone = this.deepCopy();
    const remainingDays = this.getRemainingDays();
    if (remainingDays.length === 0) {
      return clone;
    }
    const toDayKey = randomElement(Object.keys(remainingDays));
    const toDay = toDayKey.slice(0, -16);
    const toGroup = toDayKey.substr(toDayKey.length - 1);
    for (let i = 0; i < busyAssistants.length; i++) {
      const busyAssistant = busyAssistants[i];
      const double = busyAssistant.double;
      if (busyAssistant[toDay]) {
        if (double) {
          if (this.hasCapacity(toDay, 1) && this.hasCapacity(toDay, 2)) {
            const fromDayKey = busyAssistant.assignedDay;
            const fromGroup = fromDayKey.substr(fromDayKey.length - 1);
            const fromDay = fromDayKey.slice(0, -16);
            clone.reassignAssistant(busyAssistant, fromDay, toDay, 1, 1);
            clone.reassignAssistant(busyAssistant, fromDay, toDay, 2, 2);
            break;
          }
        } else if (busyAssistant.preferredGroup === toGroup) {
          const fromDayKey = busyAssistant.assignedDay;
          const fromGroup = fromDayKey.substr(fromDayKey.length - 1);
          const fromDay = fromDayKey.slice(0, -16);
          clone.reassignAssistant(busyAssistant, fromDay, toDay, fromGroup, toGroup);
          break;
        } else if (busyAssistant.preferredGroup !== toGroup) {
          continue;
        } else { // Not double, any group
          const fromDayKey = busyAssistant.assignedDay;
          const fromGroup = fromDayKey.substr(fromDayKey.length - 1);
          const fromDay = fromDayKey.slice(0, -16);
          clone.reassignAssistant(busyAssistant, fromDay, toDay, fromGroup, toGroup);
          break;
        }
      }
    }
    return clone;

  }

  getRemainingDays() {
    let remainingDays = {};

    for (let i = 0; i < weekDays.length; i++) {
      const day = weekDays[i];
      const remaining1 = this[day] - this[day + "AssistantsGroup1"].length;
      const remaining2 = this[day] - this[day + "AssistantsGroup2"].length;

      if (remaining1 > 0) {
        remainingDays[day + "AssistantsGroup1"] = remaining1;
      }

      if (remaining2 > 0) {
        remainingDays[day + "AssistantsGroup2"] = remaining2;
      }
    }

    return remainingDays;
  }

  optimizeScore() {
    let didSwap = true;

    while (didSwap) {
      let valid = this.isValid();
      didSwap = false;
      const assistants = this.queuedAssistants;

      for (let i = 0; i < assistants.length; i++) {
        const assistant = assistants[i];
        const assistantSwapped = this._swapAssistant(assistant);
        if (assistantSwapped) {
          didSwap = true;
        }
      }

      for (let i = 0; i < weekDays.length; i++) {
        const day = weekDays[i];
        const doubleAssistants = this.getAssignedAssistants(day, 1).filter(a => this._assistantHasDoublePosition(a, day));
        for (let j = 0; j < doubleAssistants.length; j++) {
          const assistant = doubleAssistants[j];
          const doubleSwapped = this._swapDoubleAssistantWithSingles(day, assistant);
          if (doubleSwapped) {
            didSwap = true;
          }
        }
      }

      const doubleAssistants = this.queuedAssistants.filter(a => a.double);

      for (let i = 0; i < doubleAssistants.length; i++) {
        const assistant = doubleAssistants[i];
        const assistantSwapped = this._swapDoubleAssistantAsSingle(assistant);
        if (assistantSwapped) {
          didSwap = true;
        }
      }

    }
  }

  _swapAssistant(assistant) {
    const availableDays = weekDays.filter(weekday => assistant[weekday]);
    for (let j = 0; j < availableDays.length; j++) {
      const day = availableDays[j];
      if (assistant.preferredGroup === null) {
        if (this._trySwap(assistant, day, 1)) {
          return true;
        }

        if (this._trySwap(assistant, day, 2)) {
          return true;
        }
      } else {
        const group = assistant.double ? 1 : assistant.preferredGroup;
        if (this._trySwap(assistant, day, group)) {
          return true;
        }
      }
    }
  }

  _trySwap(assistant, day, group) {
    const assistantsToCheck = this.getAssignedAssistants(day, group).filter(a => assistant.double === this._assistantHasDoublePosition(a, day));

    for (let k = 0; k < assistantsToCheck.length; k++) {
      const otherAssistant = assistantsToCheck[k];
      const doubleSwap = assistant.double && this._assistantHasDoublePosition(otherAssistant, day);

      if (assistant.score > otherAssistant.score) {
        this.swapFromQueue(assistant, otherAssistant, day, group, doubleSwap);
        return true;
      }
    }

    return false;
  }

  _trySwapDoubleAsSingle(assistant, day, group) {
    const assistantsToCheck = this.getAssignedAssistants(day, group).filter(a => !this._assistantHasDoublePosition(a, day));

    for (let k = 0; k < assistantsToCheck.length; k++) {
      const otherAssistant = assistantsToCheck[k];

      if (assistant.score > otherAssistant.score) {
        this.swapFromQueue(assistant, otherAssistant, day, group, false);
        return true;
      }
    }

    return false;
  }

  _swapDoubleAssistantWithSingles(day, doubleAssistant) {
    const assistantsGroup1 = this.queuedAssistants.filter(a => a[day] && !a.double && (a.preferredGroup === null || a.preferredGroup === 1));
    const assistantsGroup2 = this.queuedAssistants.filter(a => a[day] && !a.double && (a.preferredGroup === null || a.preferredGroup === 2));

    if (assistantsGroup1.length === 0) {
      return false;
    }

    const assistant1 = assistantsGroup1.reduce((prev, curr) => {
      return prev.score > curr.score ? prev : curr;
    });

    this._removeAssistantFromList(assistant1, assistantsGroup2);

    if (assistantsGroup2.length === 0) {
      return false;
    }

    const assistant2 = assistantsGroup2.reduce((prev, curr) => {
      return prev.score > curr.score ? prev : curr;
    });

    if (assistant1.score + assistant2.score > doubleAssistant.score * 2) {
      this.swapFromQueue(assistant1, doubleAssistant, day, 1, false);
      this.assignAssistantTo(assistant2, day, 2);

      return true;
    }

    return false;
  }

  _swapDoubleAssistantAsSingle(assistant) {
    const availableDays = weekDays.filter(weekday => assistant[weekday]);
    for (let j = 0; j < availableDays.length; j++) {
      const day = availableDays[j];
      if (this._trySwapDoubleAsSingle(assistant, day, 1)) {
        return true;
      }

      if (this._trySwapDoubleAsSingle(assistant, day, 2)) {
        return true;
      }
    }
  }

  generateTimeTable() {
    const schedule = this.deepCopy();

    const timeTable = {};
    for (let i = 0; i < schedule.schools.length; i++) {
      const school = schedule.schools[i];
      timeTable[school.name] = {};
      const schoolTimeTable = {};
      schoolTimeTable[1] = {};
      schoolTimeTable[2] = {};
      for (let j = 0; j < weekDays.length; j++) {
        const day = weekDays[j];
        const assistantsNeeded = school[day];
        if (assistantsNeeded > 0) {
          const group1 = [];
          const group2 = [];

          const doubleAssistants = schedule.getAssignedAssistants(day, 1).filter(a => this._assistantHasDoublePosition(a, day));
          let N = Math.min(assistantsNeeded - group1.length, doubleAssistants.length);
          for (let k = 0; k < N; k++) {
            const assistant = doubleAssistants[k];
            group1.push(assistant);
            group2.push(assistant);
            schedule.removeAssistant(assistant);
          }

          const assistantsGroup1 = schedule.getAssignedAssistants(day, 1).filter(a => !this._assistantHasDoublePosition(a, day));
          const assistantsGroup2 = schedule.getAssignedAssistants(day, 2).filter(a => !this._assistantHasDoublePosition(a, day));

          N = Math.min(assistantsNeeded - group1.length, assistantsGroup1.length);
          for (let k = 0; k < N; k++) {
            const assistant = assistantsGroup1[k];
            group1.push(assistant);
            schedule.removeAssistant(assistant);
          }

          N = Math.min(assistantsNeeded - group2.length, assistantsGroup2.length);
          for (let k = 0; k < N; k++) {
            const assistant = assistantsGroup2[k];
            group2.push(assistant);
            schedule.removeAssistant(assistant);
          }


          schoolTimeTable[1][day] = group1;
          schoolTimeTable[2][day] = group2;
        }
      }
      timeTable[school.name] = schoolTimeTable;
    }

    return timeTable;
  }

  toCSV() {
    const timeTable = this.generateTimeTable();
    let csv = '';

    const schools = Object.keys(timeTable);

    for (let i = 0; i < schools.length; i++) {
      const school = schools[i];
      const grp1 = timeTable[school][1];
      const grp2 = timeTable[school][2];

      csv += school.toUpperCase() + "\n";
      csv += "; Mandag;Tirsdag;Onsdag;Torsdag;Fredag\n";

      csv += "Gruppe 1";
      csv += this._assistantsInGroupCSV(grp1);

      csv += '\n';

      csv += "Gruppe 2";
      csv += this._assistantsInGroupCSV(grp2);

      csv += "\n\n-----------------------------------\n";

      csv += '\n\n\n';

    }

    return csv;

  }

  _assistantsInGroupCSV(group) {
    const N = weekDays.reduce((acc, day) => {
      if (!group.hasOwnProperty(day)) {
        return acc;
      }
      const assOnDay = group[day].length;

      if (assOnDay > acc) {
        return assOnDay;
      }

      return acc;
    }, 0);

    let csv = "";

    for (let k = 0; k < N; k++) {
      csv += ';';

      for (let j = 0; j < weekDays.length; j++) {
        const day = weekDays[j];
        if (group.hasOwnProperty(day) && group[day].length > k) {
          console.log(group[day], k);
          csv += group[day][k].name;
        }
        csv += ';';
      }
      csv += '\n';
    }

    return csv;
  }
}
