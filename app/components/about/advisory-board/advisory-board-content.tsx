import { AdvisoryBoardSection } from "@/app/components/about/advisory-board/advisory-board-section";
import { fetchAboutAdvisoryBoard } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

export async function AdvisoryBoardContent() {
  const t = await getTranslations("about.advisoryBoard");
  const pagesT = await getTranslations("about.pages");
  const locale = await getLocale();
  const apiData = await fetchAboutAdvisoryBoard(locale);
  const fallbackMembers = t.raw("members") as {
    id: string;
    featured?: boolean;
    role: string;
    name: string;
    image: string;
    bio: string;
  }[];

  const members = (apiData?.members ?? fallbackMembers).map((member) => ({
    ...member,
    image: `/emp/${member.image}`,
  }));

  return (
    <AdvisoryBoardSection
      members={members}
      readMore={apiData?.readMore ?? t("readMore")}
      pageTitle={pagesT("advisoryBoard")}
      isRtl={locale === "ar"}
    />
  );
}
