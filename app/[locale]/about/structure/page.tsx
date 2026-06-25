import { AboutPageShell } from "@/app/components/about/about-page-shell";
import { OperationalStructureContent } from "@/app/components/about/structure/operational-structure-content";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function StructurePage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <AboutPageShell
      titleKey="structure"
      image="/header/2.png"
      backgroundColor="#000000B8"
    >
      <OperationalStructureContent />
    </AboutPageShell>
  );
}
