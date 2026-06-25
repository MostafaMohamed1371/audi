import { PartnershipsPageClient } from "@/app/components/programs/partnerships/page/client";
import { fetchProgram } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";
import type { PartnershipsTab } from "@/lib/programs-partnerships";

const PARTNERSHIPS_HERO_OVERLAY =
  "linear-gradient(180deg, rgba(17, 31, 66, 0.3564) 0%, rgba(0, 0, 0, 0.81) 100%)";

export async function PartnershipsPageShell() {
  const pagesT = await getTranslations("programs.pages");
  const t = await getTranslations("programs.partnerships");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const apiProgram = await fetchProgram("partnerships", locale);

  const tabPreviews: Record<PartnershipsTab, string> = {
    euroArabDialogue: "/partnerships/euro-arab-dialogue.png",
    secretarySpeaks: "/partnerships/secretary-speaks.png",
    urbanAwards: "/partnerships/urban-awards.png",
    partnersGuide: "/partnerships/partners-guide.png",
  };

  const tabs = (apiProgram?.tabs ?? [
    { id: "euroArabDialogue", label: t("tabs.euroArabDialogue") },
    { id: "secretarySpeaks", label: t("tabs.secretarySpeaks") },
    { id: "urbanAwards", label: t("tabs.urbanAwards") },
    { id: "partnersGuide", label: t("tabs.partnersGuide") },
  ]).map((tab) => ({
    id: tab.id as PartnershipsTab,
    label: tab.label,
    previewImage: tabPreviews[tab.id as PartnershipsTab],
  }));

  return (
    <PartnershipsPageClient
      tabs={tabs}
      defaultTab={"euroArabDialogue" satisfies PartnershipsTab}
      isRtl={isRtl}
      hero={{
        programLabel: pagesT("programLabel"),
        title: apiProgram?.title ?? pagesT("partnerships"),
        intro: apiProgram?.heroIntro ?? t("heroIntro"),
        sectionsLabel: apiProgram?.sectionsLabel ?? t("sectionsLabel"),
        video: "/header/partnerships.mp4",
        overlay: PARTNERSHIPS_HERO_OVERLAY,
      }}
      backLabel={apiProgram?.back ?? t("back")}
      euroArabDialogue={(apiProgram?.sections.euroArabDialogue ?? t.raw("euroArabDialogue")) as never}
      secretarySpeaks={(apiProgram?.sections.secretarySpeaks ?? t.raw("secretarySpeaks")) as never}
      urbanAwards={(apiProgram?.sections.urbanAwards ?? t.raw("urbanAwards")) as never}
      partnersGuide={(apiProgram?.sections.partnersGuide ?? t.raw("partnersGuide")) as never}
    />
  );
}
