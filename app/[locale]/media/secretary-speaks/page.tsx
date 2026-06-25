import { MediaSecretarySpeaksContent } from "@/app/components/media/secretary-speaks/content";
import { MediaPageShell } from "@/app/components/media/media-page-shell";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function MediaSecretarySpeaksPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <MediaPageShell activeTab="secretarySpeaks">
      <MediaSecretarySpeaksContent />
    </MediaPageShell>
  );
}
