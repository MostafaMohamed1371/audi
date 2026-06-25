import { MediaNewsContent } from "@/app/components/media/news/content";
import { MediaPageShell } from "@/app/components/media/media-page-shell";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function MediaNewsPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <MediaPageShell activeTab="news">
      <MediaNewsContent />
    </MediaPageShell>
  );
}
