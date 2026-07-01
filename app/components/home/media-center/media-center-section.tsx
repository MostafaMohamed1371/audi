import Image from "next/image";
import { getLocale, getTranslations } from "next-intl/server";
import { Link, type AppHref } from "@/i18n/routing";
import { mediaArticleHref } from "@/lib/hrefs";
import { getNewsSlugs } from "@/lib/media-slugs";
import { ButtonLink } from "@/app/components/ui/button";
import { ChevronLeft } from "lucide-react";
import { MediaFeaturedCarousel } from "@/app/components/home/media-center/media-featured-carousel";
import type { HomePayload } from "@/lib/api";
import { resolveImageSrc } from "@/lib/image-src";

const blogImages = ["1.png", "2.png", "3.png", "4.png"] as const;

function SectionTitle({ title }: { title: string }) {
  const words = title.trim().split(/\s+/);
  const firstWord = words.shift() ?? title;
  const restText = words.join(" ");

  return (
    <h2 className="text-2xl font-bold leading-none text-white sm:text-3xl lg:text-4xl">
      <span className="relative inline-block">
        <span
          className="pointer-events-none absolute start-0 top-1/2 -z-0 h-[23px] w-[70px] -translate-y-1/2 rounded-[41px] bg-[#00709E5C] sm:h-[26px] sm:w-[78px] sm:rounded-[46px]"
          aria-hidden
        />
        <span className="relative z-10">{firstWord}</span>
      </span>
      {restText ? <span> {restText}</span> : null}
    </h2>
  );
}

type MediaItem = {
  title: string;
  description: string;
  date: string;
  href: AppHref;
};

export async function MediaCenterSection({
  mediaCenter,
}: {
  mediaCenter?: HomePayload["mediaCenter"];
} = {}) {
  const t = await getTranslations("home.mediaCenter");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const localeKey = locale as "ar" | "en";
  const newsSlugs = getNewsSlugs(localeKey, 4);

  const mapApiItem = (
    item: Record<string, unknown>,
    index: number,
    slugFallback: string[],
  ): MediaItem => ({
    title: String(item.title ?? ""),
    description: String(item.description ?? ""),
    date: String(item.date ?? ""),
    image:
      resolveImageSrc(String(item.image ?? "")) ||
      `/blog/${blogImages[index] ?? blogImages[0]}`,
    href: item.slug
      ? mediaArticleHref(String(item.slug), "news")
      : mediaArticleHref(slugFallback[index] ?? slugFallback[0], "news"),
  });

  const slideContent = t.raw("slides") as Omit<MediaItem, "href">[];
  const slides = mediaCenter?.featured?.length
    ? mediaCenter.featured.map((item, index) =>
        mapApiItem(item, index, newsSlugs),
      )
    : slideContent.map((slide, index) => ({
        ...slide,
        image: blogImages[index] ?? blogImages[0],
        href: mediaArticleHref(newsSlugs[index] ?? newsSlugs[0], "news"),
      }));

  const itemContent = t.raw("items") as Omit<MediaItem, "href">[];
  const itemSlugs = getNewsSlugs(localeKey, 6).slice(1, 5);
  const items = mediaCenter?.items?.length
    ? mediaCenter.items.map((item, index) =>
        mapApiItem(item, index, itemSlugs),
      )
    : itemContent.map((item, index) => ({
        ...item,
        image: blogImages[index] ?? blogImages[0],
        href: mediaArticleHref(itemSlugs[index] ?? itemSlugs[0], "news"),
      }));

  return (
    <section
      id="media"
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-secondary py-12 sm:py-16 lg:py-24"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div
          className={`mb-8 sm:mb-12 ${isRtl ? "text-right" : "text-left"}`}
        >
          <SectionTitle title={mediaCenter?.title ?? t("title")} />
          <p className="mt-3 max-w-2xl text-sm leading-7 text-white/75 sm:mt-4 sm:text-base sm:leading-8">
            {mediaCenter?.subtitle ?? t("subtitle")}
          </p>
        </div>

        <MediaFeaturedCarousel
          slides={slides}
          readMore={mediaCenter?.readMore ?? t("readMore")}
          isRtl={isRtl}
        />

        <div className="mt-6 grid gap-4 sm:mt-8 sm:gap-6 md:grid-cols-2 lg:gap-8">
          {items.slice(0, 2).map((item) => (
            <article
              key={item.title}
              className="flex flex-col overflow-hidden rounded-[24px] bg-white shadow-[1px_1px_18.6px_0px_#111F421C] sm:flex-row sm:rounded-[30px]"
            >
              <div className="relative m-4 aspect-[16/10] w-auto shrink-0 overflow-hidden rounded-2xl sm:m-5 sm:me-0 sm:aspect-4/5 sm:w-[42%] sm:min-w-[140px] sm:self-stretch sm:rounded-[16px]">
                <Image
                  src={item.image}
                  alt={item.title}
                  fill
                  className="object-cover"
                  sizes="(max-width: 640px) 100vw, (max-width: 768px) 40vw, 280px"
                />
              </div>

              <div
                className={`flex flex-1 flex-col justify-between p-4 sm:p-6 ${isRtl ? "items-start text-right" : "items-start text-left"}`}
              >
                <div>
                  <h3 className="mb-2 text-base font-bold leading-snug text-secondary sm:mb-3 sm:text-lg">
                    {item.title}
                  </h3>
                  <p className="line-clamp-3 text-sm leading-6 text-[#4d5a6f] sm:leading-7">
                    {item.description}
                  </p>
                </div>

                <div className="mt-4 flex w-full flex-wrap items-center justify-between gap-3 text-xs sm:mt-5 sm:gap-4 sm:text-sm">
                  <Link
                    href={item.href}
                    className="inline-flex items-center gap-1 font-medium text-primary hover:text-primary/80"
                  >
                    {mediaCenter?.readMore ?? t("readMore")}
                    <ChevronLeft className="size-3.5" />
                  </Link>
                  <time className="shrink-0 font-medium tracking-wide text-primary uppercase">
                    {item.date}
                  </time>
                </div>
              </div>
            </article>
          ))}
        </div>

        <div className="mt-8 flex justify-center sm:mt-12">
          <ButtonLink
            variant="outline"
            size="lg"
            className="w-full min-w-0 border-white/60 bg-transparent px-8 text-white hover:bg-white/10 hover:text-white sm:w-auto sm:min-w-[220px] sm:px-10"
            render={<Link href="/media/news" />}
          >
            {mediaCenter?.viewAll ?? t("viewAll")}
          </ButtonLink>
        </div>
      </div>
    </section>
  );
}
