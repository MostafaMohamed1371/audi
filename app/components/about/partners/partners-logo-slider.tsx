"use client";

import Image from "next/image";
import Marquee from "react-fast-marquee";
import { resolveImageSrc } from "@/lib/image-src";

type PartnerLogo = {
  image: string;
  name: string;
};

type Props = {
  logos: PartnerLogo[];
  isRtl: boolean;
};

function LogoItem({ logo }: { logo: PartnerLogo }) {
  return (
    <div className="mx-5 flex h-28 w-48 shrink-0 items-center justify-center ">
      <Image
        src={resolveImageSrc(logo.image, "/client")}
        alt={logo.name}
        width={240}
        height={120}
        className="max-h-24 w-auto object-contain sm:max-h-28 lg:max-h-32"
      />
    </div>
  );
}

export function PartnersLogoSlider({ logos, isRtl }: Props) {
  if (logos.length === 0) return null;

  return (
    <section
      dir={isRtl ? "rtl" : "ltr"}
      className="relative z-10 -mt-16 sm:-mt-20"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div className="overflow-hidden rounded-3xl bg-white px-4 py-10 shadow-[1px_1px_18.6px_0px_#111F421C] sm:px-6 sm:py-12" dir="ltr">
          <Marquee
            direction={isRtl ? "right" : "left"}
            speed={40}
            pauseOnHover
            gradient={false}
            autoFill
          >
            {logos.map((logo) => (
              <LogoItem key={logo.image} logo={logo} />
            ))}
          </Marquee>
        </div>
      </div>
    </section>
  );
}
