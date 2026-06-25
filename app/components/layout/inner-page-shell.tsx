import Image from "next/image";
import { getLocale } from "next-intl/server";
import { cn } from "@/lib/utils";

const HERO_OVERLAY =
  "linear-gradient(180deg, rgba(17, 31, 66, 0.44) 0%, #000000 100%)";

type Props = {
  title: string;
  subtitle?: string;
  children: React.ReactNode;
};

export async function InnerPageShell({ title, subtitle, children }: Props) {
  const locale = await getLocale();
  const isRtl = locale === "ar";

  return (
    <div className="bg-background">
      <section className="relative overflow-hidden">
        <div className="relative min-h-[280px] sm:min-h-[320px] lg:min-h-[360px]">
          <Image
            src="/header/our-sources.png"
            alt=""
            fill
            priority
            className="object-cover"
            sizes="100vw"
          />

          <div
            className="absolute inset-0"
            style={{ background: HERO_OVERLAY }}
            aria-hidden
          />

          <div className="relative z-10 mx-auto flex min-h-[280px] max-w-7xl flex-col justify-end px-4 pb-12 pt-32 sm:min-h-[320px] sm:px-6 sm:pb-14 sm:pt-36 lg:min-h-[360px] lg:pb-16 lg:pt-40">
            <div
              dir={isRtl ? "rtl" : "ltr"}
              className={cn("max-w-3xl space-y-3 text-start")}
            >
              <h1 className="text-3xl font-bold text-white sm:text-4xl lg:text-5xl">
                {title}
              </h1>
              {subtitle ? (
                <p className="text-sm leading-8 text-white/90 sm:text-base sm:leading-9">
                  {subtitle}
                </p>
              ) : null}
            </div>
          </div>
        </div>
      </section>

      <div className="px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
        <div className="mx-auto max-w-7xl">{children}</div>
      </div>
    </div>
  );
}
