import { Footer } from "@/app/components/layout/footer";
import { Navbar } from "@/app/components/layout/navbar";
import { DirectionProvider } from "@/app/components/ui/direction";
import { rbFont } from "@/app/fonts";
import { routing } from "@/i18n/routing";
import type { Metadata } from "next";
import { NextIntlClientProvider } from "next-intl";
import { getMessages, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { Suspense } from "react";
import "../globals.css";

export function generateStaticParams() {
  return routing.locales.map((locale) => ({ locale }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ locale: string }>;
}): Promise<Metadata> {
  const { locale } = await params;
  const isArabic = locale === "ar";

  return {
    title: {
      default: isArabic
        ? "المعهد العربي لإنماء المدن"
        : "Arab Urban Development Institute",
      template: isArabic
        ? "%s | المعهد العربي لإنماء المدن"
        : "%s | Arab Urban Development Institute",
    },
    description: isArabic
      ? "المعهد العربي لإنماء المدن — مؤسسة رائدة في مجال التنمية العمرانية العربية"
      : "Arab Urban Development Institute — a leading institution in Arab urban development",
  };
}

export default async function LocaleLayout({
  children,
  params,
}: {
  children: React.ReactNode;
  params: Promise<{ locale: string }>;
}) {
  const { locale } = await params;

  if (!routing.locales.includes(locale as any)) {
    notFound();
  }

  setRequestLocale(locale);

  const messages = await getMessages();
  const direction = locale === "ar" ? "rtl" : "ltr";

  return (
    <html
      id="top"
      lang={locale}
      dir={direction}
      className={`${rbFont.variable} ${rbFont.className} h-full antialiased`}
      suppressHydrationWarning
    >
      <body
        className="min-h-full flex flex-col bg-background text-foreground font-sans"
        suppressHydrationWarning
      >
        <DirectionProvider direction={direction}>
          <NextIntlClientProvider messages={messages}>
            <Suspense fallback={null}>
              <Navbar />
            </Suspense>
            <main className="flex-1">{children}</main>
            <Footer />
          </NextIntlClientProvider>
        </DirectionProvider>
      </body>
    </html>
  );
}
