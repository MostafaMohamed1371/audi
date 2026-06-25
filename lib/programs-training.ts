export type TrainingTab =
  | "trainingPrograms"
  | "consulting"
  | "executive"
  | "experts";

export const TRAINING_TABS: TrainingTab[] = [
  "trainingPrograms",
  "consulting",
  "executive",
  "experts",
];

export function isTrainingTab(value: string): value is TrainingTab {
  return TRAINING_TABS.includes(value as TrainingTab);
}
