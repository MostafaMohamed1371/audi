import { AboutPageShell } from "@/app/components/about/about-page-shell";
import { VisionMissionContent } from "@/app/components/about/vision-mission/vision-mission-content";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function VisionMissionPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <AboutPageShell titleKey="visionMission">
      <VisionMissionContent />
    </AboutPageShell>
  );
}
