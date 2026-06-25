import { AboutPageShell } from "@/app/components/about/about-page-shell";
import { AdvisoryBoardContent } from "@/app/components/about/advisory-board/advisory-board-content";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function AdvisoryBoardPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <AboutPageShell
      titleKey="advisoryBoard"
      image="/header/2.png"
      backgroundColor="#000000B8"
    >
      <AdvisoryBoardContent />
    </AboutPageShell>
  );
}
