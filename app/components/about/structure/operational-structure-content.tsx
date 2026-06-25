import Image from "next/image";
import { fetchAboutStructure } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

export async function OperationalStructureContent() {
  const t = await getTranslations("about.structure");
  const locale = await getLocale();
  const apiData = await fetchAboutStructure(locale);

  return (
    <section className="bg-white py-16 sm:py-20 lg:py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div className="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
          <Image
            src={apiData?.imageUrl ?? "/operational-structure.png"}
            alt={apiData?.imageAlt ?? t("imageAlt")}
            width={1200}
            height={1400}
            className="mx-auto h-auto w-full min-w-[680px] max-w-5xl"
            sizes="(max-width: 1024px) 100vw, 1024px"
            priority
          />
        </div>
      </div>
    </section>
  );
}
