export type TrainingProgramsContent = {
  title: string;
  intro: string;
  formatsTitle: string;
  formats: string[];
  coursesTitle: string;
  courses: { title: string; count: string }[];
  image?: string;
  heroImage?: string;
  coursesImage?: string;
};

export type ConsultingContent = {
  title: string;
  intro: string;
  nav: string[];
  sections: { title: string; description: string }[];
  image?: string;
  detailImage?: string;
};

export type ExecutiveContent = {
  title: string;
  intro: string;
  offersTitle: string;
  programs: string[];
  topicsTitle: string;
  topics: { title: string; image: string }[];
  heroVideo?: string;
};

export type ExpertsContent = {
  title: string;
  experts: { name: string; specialty: string; image: string }[];
};

export type TrainingPanelProps = {
  isRtl: boolean;
  backLabel: string;
  onBack: () => void;
};
