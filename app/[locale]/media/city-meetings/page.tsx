import { MediaCityMeetingsContent } from "@/app/components/media/city-meetings/content";
import { MediaPageShell } from "@/app/components/media/media-page-shell";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function MediaCityMeetingsPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <MediaPageShell activeTab="cityMeetings">
      <MediaCityMeetingsContent />
    </MediaPageShell>
  );
}
