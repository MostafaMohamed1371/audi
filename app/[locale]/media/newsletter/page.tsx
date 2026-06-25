import { MediaNewsletterContent } from "@/app/components/media/newsletter/content";
import { MediaPageShell } from "@/app/components/media/media-page-shell";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function MediaNewsletterPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <MediaPageShell activeTab="newsletter">
      <MediaNewsletterContent />
    </MediaPageShell>
  );
}
