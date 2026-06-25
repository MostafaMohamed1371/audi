import { AboutPageShell } from "@/app/components/about/about-page-shell";
import { TeamContent } from "@/app/components/about/team/team-content";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function TeamPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <AboutPageShell
      titleKey="team"
      image="/header/2.png"
      backgroundColor="#000000B8"
    >
      <TeamContent />
    </AboutPageShell>
  );
}
