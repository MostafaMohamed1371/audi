import { FocusAreaBanner } from "@/app/components/strategy/focus-areas/focus-area-banner";
import type { FocusAreasMessages } from "@/lib/focus-areas";
import { getLocale } from "next-intl/server";

type Props = {
  content: FocusAreasMessages;
};

export async function FocusAreasPage({ content }: Props) {
  const locale = await getLocale();
  const isRtl = locale === "ar";

  return (
    <div className="bg-background">
      <section className="px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
        <h1
          dir={isRtl ? "rtl" : "ltr"}
          className="mx-auto max-w-7xl text-center text-3xl font-bold text-secondary sm:text-4xl lg:text-5xl"
        >
          {content.pages.title}
        </h1>
      </section>

      <section className="space-y-0">
        {content.items.map((area) => (
          <FocusAreaBanner
            key={area.slug}
            area={area}
            viewMoreLabel={content.pages.viewMore}
          />
        ))}
      </section>
    </div>
  );
}
