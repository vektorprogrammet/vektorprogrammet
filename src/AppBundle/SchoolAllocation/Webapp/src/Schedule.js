function randomElement(arr) {
  const index = Math.floor(Math.random()*arr.length);

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

  swapFromQueue(assistantInQueue, assignedAssistant, day, group) {
    if (assistantInQueue.double) {
      this.assignAssistantTo(assistantInQueue, day, 1);
      this.assignAssistantTo(assistantInQueue, day, 2);
      this._removeAssistantFromList(assignedAssistant, this[day + "AssistantsGroup" + 1]);
      this._removeAssistantFromList(assignedAssistant, this[day + "AssistantsGroup" + 2]);
    } else {
      this.assignAssistantTo(assistantInQueue, day, group);
      this._removeAssistantFromList(assignedAssistant, this[day + "AssistantsGroup" + group]);
    }
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
    for (let i = 0; i < weekDays.length; i++){
      const day = weekDays[i];
      count += this[day + "AssistantsGroup1"].length;
      count += this[day + "AssistantsGroup2"].length;
    }
    return count;
  }

  fitness() {
    return this.scheduledAssistantsCount();
  }

  isValid() {
    for (let group = 1; group <= 2; group++) {
      for (let i = 0; i < weekDays.length; i++) {
        const day = weekDays[i];
        const assistantsKey = day + "AssistantsGroup" + group;
        const assistants = this[assistantsKey];
        for (let j = 0; j < assistants.length; j++) {
          const assistant = assistants[j];
          if (assistant.preferredGroup !== null && assistant.preferredGroup !== group) {
            return false;
          }
        }
      }
    }

    return true;
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
			const fromGroup = fromDayKey.substr(fromDayKey.length -1 );
			const fromDay = fromDayKey.slice(0, -16);
			clone.reassignAssistant(busyAssistant, fromDay, toDay, 1, 1);
			clone.reassignAssistant(busyAssistant, fromDay, toDay, 2, 2);
			break;
		    }
		} else if (busyAssistant.preferredGroup === toGroup){
		    const fromDayKey = busyAssistant.assignedDay;
		    const fromGroup = fromDayKey.substr(fromDayKey.length -1 );
		    const fromDay = fromDayKey.slice(0, -16);
		    clone.reassignAssistant(busyAssistant, fromDay, toDay, fromGroup, toGroup);
		    break;
		} else if (busyAssistant.preferredGroup !== toGroup){
		    continue;
		} else { // Not double, any group
		    const fromDayKey = busyAssistant.assignedDay;
		    const fromGroup = fromDayKey.substr(fromDayKey.length -1 );
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
}
