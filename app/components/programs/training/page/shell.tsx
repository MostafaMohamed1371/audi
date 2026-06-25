import { TrainingPageClient } from "@/app/components/programs/training/page/client";
import { fetchProgram } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";
import type { TrainingTab } from "@/lib/programs-training";

const TRAINING_HERO_OVERLAY =
  "linear-gradient(180deg, rgba(17, 31, 66, 0.3564) 0%, rgba(0, 0, 0, 0.81) 100%)";

export async function TrainingPageShell() {
  const pagesT = await getTranslations("programs.pages");
  const t = await getTranslations("programs.training");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const apiProgram = await fetchProgram("training", locale);

  const tabVideos: Record<TrainingTab, string> = {
    trainingPrograms: "/icons/program/1.mp4",
    consulting: "/icons/program/2.mp4",
    executive: "/icons/program/3.mp4",
    experts: "/icons/program/5.mp4",
  };

  const tabs = (apiProgram?.tabs ?? [
    { id: "trainingPrograms", label: t("tabs.trainingPrograms") },
    { id: "consulting", label: t("tabs.consulting") },
    { id: "executive", label: t("tabs.executive") },
    { id: "experts", label: t("tabs.experts") },
  ]).map((tab) => ({
    id: tab.id as TrainingTab,
    label: tab.label,
    video: tabVideos[tab.id as TrainingTab],
  }));

  return (
    <TrainingPageClient
      tabs={tabs}
      defaultTab={"trainingPrograms" satisfies TrainingTab}
      isRtl={isRtl}
      hero={{
        programLabel: pagesT("programLabel"),
        title: apiProgram?.title ?? pagesT("training"),
        intro: apiProgram?.heroIntro ?? t("heroIntro"),
        sectionsLabel: apiProgram?.sectionsLabel ?? t("sectionsLabel"),
        video: "/header/citysupport.mp4",
        overlay: TRAINING_HERO_OVERLAY,
      }}
      backLabel={apiProgram?.back ?? t("back")}
      trainingPrograms={(apiProgram?.sections.trainingPrograms ?? t.raw("trainingPrograms")) as never}
      consulting={(apiProgram?.sections.consulting ?? t.raw("consulting")) as never}
      executive={(apiProgram?.sections.executive ?? t.raw("executive")) as never}
      experts={(apiProgram?.sections.experts ?? t.raw("experts")) as never}
    />
  );
}
