import { PartnersContent } from "@/app/components/about/partners/partners-content";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function PartnersPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return <PartnersContent />;
}
