import { AboutInstituteContent } from "@/app/components/about/institute/about-institute-content";
import { AboutPageShell } from "@/app/components/about/about-page-shell";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function AboutInstitutePage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <AboutPageShell titleKey="institute">
      <AboutInstituteContent />
    </AboutPageShell>
  );
}
