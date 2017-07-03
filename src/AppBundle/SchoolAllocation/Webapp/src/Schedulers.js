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

function shuffle(a) {
  for (let i = a.length; i; i--) {
    let j= Math.floor(Math.random() * i);
    [a[i-1], a[j]] = [a[j], a[i-1]];
  }
}
