import { CareerDetailContent } from "@/app/components/careers/career-detail-content";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string; id: string }>;
};

export default async function CareerDetailPage({ params }: Props) {
  const { locale, id } = await params;
  setRequestLocale(locale);

  return <CareerDetailContent id={id} />;
}
