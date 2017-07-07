const weekDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
const dt = 0.0005;
const mutationCount = 20;
const calculationsPerRenderFrame = 500;

export function SAOptimize(schedule, done) {
  let temp = 1;
  let bestSchedule = schedule;
  let currentSchedule = schedule;

  setTimeout(function a() {
    for (let j = 0; j < calculationsPerRenderFrame; j++) {

      const mutations = [currentSchedule.mutate()];
      let bestMutation = mutations[0];
      let bestFitness = bestMutation.fitness();
      for (let i = 0; i < mutationCount - 1; i++) {
        const mutation = scheduleGreedily(currentSchedule.mutate());
        mutations.push(mutation);
        if (mutation.fitness() > bestFitness) {
          bestFitness = mutation.fitness();
          bestMutation = mutation;
        }
      }
      if (bestFitness > bestSchedule.fitness()) {
        bestSchedule = scheduleGreedily(bestMutation);
      }

      const q = (bestFitness - currentSchedule.fitness()) / currentSchedule.fitness();
      const p = Math.min(1, Math.exp(-q / temp));
      const x = Math.random();
      if (x > p) {
        currentSchedule = bestMutation;
      } else {
        const idx = Math.floor(Math.random() * mutations.length);
        currentSchedule = mutations[idx];
      }

      temp -= dt;
    }
    if (temp > 0) {
      setTimeout(a, 0);
    } else {
      setTimeout(() => {
        done(bestSchedule);
      }, 300)
    }
  }, 0);
}

export function scheduleGreedily(schedule) {
  shuffle(weekDays);
  const assistants = schedule.queuedAssistants;
  let j = 0;
  while (j < assistants.length) {
    const assistant = assistants[j];
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
    j++;
  }

  return schedule;
}

export function optimizeScore(schedule) {
  let didSwap = true;
  while (didSwap) {
    didSwap = false;
    const assistants = schedule.queuedAssistants;
    for (let i = 0; i < assistants.length; i++) {
      const assistant = assistants[i];
      const assistantSwapped = _swapAssistant(schedule, assistant);
      if (assistantSwapped) {
        didSwap = true;
      }
    }
  }

  return schedule;
}

function _swapAssistant(schedule, assistant) {
  const availableDays = weekDays.filter(weekday => assistant[weekday]);
  for (let j = 0; j < availableDays.length; j++) {
    const day = availableDays[j];
    if (assistant.preferredGroup === null) {
      if (_trySwap(schedule, assistant, day, 1)) {
        return true;
      }

      if (_trySwap(schedule, assistant, day, 2)) {
        return true;
      }
    } else {
      if (_trySwap(schedule, assistant, day, assistant.preferredGroup)) {
        return true;
      }
    }
  }
}

function _trySwap(schedule, assistant, day, group) {
  if (assistant.double) {
    group = 1;
  }
  const assistantsToCheck = schedule.getAssignedAssistants(day, group);

  for (let k = 0; k < assistantsToCheck.length; k++) {
    const otherAssistant = assistantsToCheck[k];

    if (assistant.double === otherAssistant.double && assistant.score > otherAssistant.score) {
      schedule.swapFromQueue(assistant, otherAssistant, day, group);
      return true;
    }
  }

  return false;
}

export function mapScheduledAssistantsToSchools(schools, schedule) {
  schedule = schedule.deepCopy();

  const timeTable = {};
  for (let i = 0; i < schools.length; i++) {
    const school = schools[i];
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

        const doubleAssistants = schedule.getAssignedAssistants(day, 1).filter(a => a.double);
        let N = Math.min(assistantsNeeded - group1.length, doubleAssistants.length);
        for (let k = 0; k < N; k++) {
          const assistant = doubleAssistants[k];
          group1.push(assistant);
          group2.push(assistant);
          schedule.removeAssistant(assistant);
        }

        const assistantsGroup1 = schedule.getAssignedAssistants(day, 1).filter(a => !a.double);
        const assistantsGroup2 = schedule.getAssignedAssistants(day, 2).filter(a => !a.double);

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

  console.log(timeTable);
  return timeTable;
}

function shuffle(a) {
  for (let i = a.length; i; i--) {
    let j= Math.floor(Math.random() * i);
    [a[i-1], a[j]] = [a[j], a[i-1]];
  }
}
