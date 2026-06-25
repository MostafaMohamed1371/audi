"use client";

import type { TrainingTab } from "@/lib/programs-training";
import { ConsultingPanel } from "@/app/components/programs/training/sections/consulting/panel";
import { ExecutivePanel } from "@/app/components/programs/training/sections/executive/panel";
import { ExpertsPanel } from "@/app/components/programs/training/sections/experts/panel";
import { TrainingProgramsPanel } from "@/app/components/programs/training/sections/training-programs/panel";
import type {
  ConsultingContent,
  ExecutiveContent,
  ExpertsContent,
  TrainingProgramsContent,
} from "@/app/components/programs/training/shared/types";

type Props = {
  activeTab: TrainingTab;
  isRtl: boolean;
  backLabel: string;
  onBack: () => void;
  trainingPrograms: TrainingProgramsContent;
  consulting: ConsultingContent;
  executive: ExecutiveContent;
  experts: ExpertsContent;
};

export function TrainingTabPanels({
  activeTab,
  isRtl,
  backLabel,
  onBack,
  trainingPrograms,
  consulting,
  executive,
  experts,
}: Props) {
  switch (activeTab) {
    case "trainingPrograms":
      return (
        <TrainingProgramsPanel
          content={trainingPrograms}
          isRtl={isRtl}
          backLabel={backLabel}
          onBack={onBack}
        />
      );
    case "consulting":
      return (
        <ConsultingPanel
          content={consulting}
          isRtl={isRtl}
          backLabel={backLabel}
          onBack={onBack}
        />
      );
    case "executive":
      return (
        <ExecutivePanel
          content={executive}
          isRtl={isRtl}
          backLabel={backLabel}
          onBack={onBack}
        />
      );
    case "experts":
      return (
        <ExpertsPanel
          content={experts}
          isRtl={isRtl}
          backLabel={backLabel}
          onBack={onBack}
        />
      );
    default:
      return null;
  }
}
