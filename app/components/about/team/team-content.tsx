import { TeamSection } from "@/app/components/about/team/team-section";
import { fetchAboutTeam } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

export async function TeamContent() {
  const t = await getTranslations("about.team");
  const pagesT = await getTranslations("about.pages");
  const locale = await getLocale();
  const apiData = await fetchAboutTeam(locale);
  const fallbackSections = t.raw("sections") as {
    id: string;
    title: string;
    members: {
      id: string;
      role: string;
      name: string;
      image: string;
      bio: string;
    }[];
  }[];

  const sections = (apiData?.sections ?? fallbackSections).map((section) => ({
    ...section,
    members: section.members.map((member) => ({
      ...member,
      image: `/emp/${member.image}`,
    })),
  }));

  return (
    <TeamSection
      sections={sections}
      readMore={apiData?.readMore ?? t("readMore")}
      pageTitle={pagesT("team")}
      isRtl={locale === "ar"}
    />
  );
}
