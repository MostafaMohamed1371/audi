import { UrbanPoliciesPageClient } from "@/app/components/programs/urban-policies/page/client";
import type { DevelopmentPortalContent } from "@/app/components/programs/urban-policies/shared/types";
import { fetchAllDirectoryRows, fetchProgram } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";
import type { UrbanPoliciesTab } from "@/lib/programs-urban-policies";

const URBAN_POLICIES_HERO_OVERLAY =
  "linear-gradient(180deg, rgba(17, 31, 66, 0.3564) 0%, rgba(0, 0, 0, 0.81) 100%)";

export async function UrbanPoliciesPageShell() {
  const pagesT = await getTranslations("programs.pages");
  const t = await getTranslations("programs.urbanPolicies");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const apiProgram = await fetchProgram("urban-policies", locale);
  const directoryRows = await fetchAllDirectoryRows(locale);

  const tabPreviews: Record<UrbanPoliciesTab, string> = {
    developmentPortal: "/urban-policies/1.gif",
    developmentIndex: "/urban-policies/2.gif",
    innovationLab: "/urban-policies/3.gif",
    practiceReports: "/urban-policies/4.gif",
  };

  const tabs = (apiProgram?.tabs ?? [
    { id: "developmentPortal", label: t("tabs.developmentPortal") },
    { id: "developmentIndex", label: t("tabs.developmentIndex") },
    { id: "innovationLab", label: t("tabs.innovationLab") },
    { id: "practiceReports", label: t("tabs.practiceReports") },
  ]).map((tab) => ({
    id: tab.id as UrbanPoliciesTab,
    label: tab.label,
    previewImage: tabPreviews[tab.id as UrbanPoliciesTab],
  }));

  const fallbackPortal = t.raw("developmentPortal") as DevelopmentPortalContent;
  const apiPortal = apiProgram?.sections.developmentPortal as DevelopmentPortalContent | undefined;
  const developmentPortal: DevelopmentPortalContent = apiPortal
    ? {
        ...apiPortal,
        directory: {
          ...apiPortal.directory,
          rows: {
            cities: (directoryRows.cities ?? fallbackPortal.directory.rows.cities) as DevelopmentPortalContent["directory"]["rows"]["cities"],
            projects: (directoryRows.projects ?? fallbackPortal.directory.rows.projects) as DevelopmentPortalContent["directory"]["rows"]["projects"],
            organizations: (directoryRows.organizations ?? fallbackPortal.directory.rows.organizations) as DevelopmentPortalContent["directory"]["rows"]["organizations"],
            publications: (directoryRows.publications ?? fallbackPortal.directory.rows.publications) as DevelopmentPortalContent["directory"]["rows"]["publications"],
          },
        },
      }
    : fallbackPortal;

  return (
    <UrbanPoliciesPageClient
      tabs={tabs}
      defaultTab={"developmentPortal" satisfies UrbanPoliciesTab}
      isRtl={isRtl}
      hero={{
        programLabel: pagesT("programLabel"),
        title: apiProgram?.title ?? pagesT("urbanPolicies"),
        intro: apiProgram?.heroIntro ?? t("heroIntro"),
        sectionsLabel: apiProgram?.sectionsLabel ?? t("sectionsLabel"),
        background: "/urban-policies/header.gif",
        backgroundKind: "gif",
        overlay: URBAN_POLICIES_HERO_OVERLAY,
      }}
      backLabel={apiProgram?.back ?? t("back")}
      developmentPortal={developmentPortal}
      developmentIndex={(apiProgram?.sections.developmentIndex ?? t.raw("developmentIndex")) as never}
      innovationLab={(apiProgram?.sections.innovationLab ?? t.raw("innovationLab")) as never}
      practiceReports={(apiProgram?.sections.practiceReports ?? t.raw("practiceReports")) as never}
    />
  );
}
