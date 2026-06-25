import { Strategy2025Content } from "@/app/components/strategy/strategy-2025/strategy-2025-content";
import { setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function Strategy2025Page({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return <Strategy2025Content />;
}
