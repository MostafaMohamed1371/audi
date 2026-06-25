import { ResourcesContent } from "@/app/components/resources/resources-content";
import { ResourcesPageShell } from "@/app/components/resources/resources-page-shell";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function ResourcesPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <ResourcesPageShell>
      <ResourcesContent />
    </ResourcesPageShell>
  );
}
