"use client";

import { MediaHeroVideo } from "@/app/components/media/media-hero-video";

type Props = {
  src: string;
  kind: "video" | "gif";
};

export function MediaHeroBackground({ src, kind }: Props) {
  if (kind === "video") {
    return <MediaHeroVideo src={src} />;
  }

  return (
    // eslint-disable-next-line @next/next/no-img-element
    <img
      src={src}
      alt=""
      className="absolute inset-0 h-full w-full object-cover"
      aria-hidden
    />
  );
}
