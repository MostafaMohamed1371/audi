import { cn } from "@/lib/utils";
import Image from "next/image";

type PartnerLogo = {
  image: string;
  name: string;
};

type Category = {
  id: string;
  title: string;
  logos: PartnerLogo[];
};

type Props = {
  categories: Category[];
  isRtl: boolean;
};

function PartnerLogoCard({ logo }: { logo: PartnerLogo }) {
  return (
    <article className="flex aspect-4/3 items-center justify-center rounded-xl border border-primary/20 bg-white p-4 shadow-[0_2px_12px_rgba(17,31,66,0.04)] sm:rounded-2xl sm:p-5">
      <Image
        src={`/client/${logo.image}`}
        alt={logo.name}
        width={120}
        height={72}
        className="max-h-14 w-auto object-contain sm:max-h-16"
      />
    </article>
  );
}

export function PartnersCategories({ categories, isRtl }: Props) {
  return (
    <section
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-white py-12 sm:py-16 lg:py-20"
    >
      <div className="mx-auto max-w-7xl space-y-12 px-4 sm:space-y-16 sm:px-6 lg:space-y-20">
        {categories.map((category) => (
          <div key={category.id}>
            <h2
              className={cn(
                "mb-8 flex items-center gap-3 text-xl font-bold text-primary sm:mb-10 sm:text-2xl",
                isRtl ? "flex-row-reverse justify-end text-right" : "text-left",
              )}
            >
              {category.title}
              <span
                className="size-2 shrink-0 rounded-full bg-primary"
                aria-hidden
              />
            </h2>

            <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-5 lg:grid-cols-6 xl:grid-cols-7">
              {category.logos.map((logo, index) => (
                <PartnerLogoCard
                  key={`${category.id}-${logo.image}-${index}`}
                  logo={logo}
                />
              ))}
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}
