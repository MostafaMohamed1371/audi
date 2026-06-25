import { InstituteTasksSection } from "@/app/components/about/institute/institute-tasks-section";
import { InstituteValuesSection } from "@/app/components/about/vision-mission/institute-values-section";
import { VisionMissionSection } from "@/app/components/about/vision-mission/vision-mission-section";
import { fetchAboutVisionMission } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

type GoalItem = {
  description: string;
};

type ValueItem = {
  title: string;
  description: string;
};

export async function VisionMissionContent() {
  const t = await getTranslations("about.visionMission");
  const tGoals = await getTranslations("about.goals");
  const tValues = await getTranslations("about.values");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const apiData = await fetchAboutVisionMission(locale);

  const goalItems = (apiData?.goals.items ?? tGoals.raw("items")) as GoalItem[];
  const valueItems = (apiData?.values.items ?? tValues.raw("items")) as ValueItem[];

  return (
    <>
      <VisionMissionSection
        isRtl={isRtl}
        vision={{
          title: apiData?.vision.title ?? t("visionTitle"),
          text: apiData?.vision.text ?? t("visionText"),
          readMore: apiData?.vision.readMore ?? t("readMore"),
          image: apiData?.vision.image ?? "/vision-mission/1.png",
        }}
        mission={{
          title: apiData?.mission.title ?? t("missionTitle"),
          text: apiData?.mission.text ?? t("missionText"),
          readMore: apiData?.mission.readMore ?? t("readMore"),
          image: apiData?.mission.image ?? "/vision-mission/2.png",
        }}
      />

      <InstituteTasksSection
        title={apiData?.goals.title ?? tGoals("title")}
        items={goalItems}
        showTitleAccent
      />

      <InstituteValuesSection
        title={apiData?.values.title ?? tValues("title")}
        items={valueItems}
        isRtl={isRtl}
      />
    </>
  );
}
