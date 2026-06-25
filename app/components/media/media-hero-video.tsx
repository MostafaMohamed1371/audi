"use client";

type Props = {
  src: string;
};

export function MediaHeroVideo({ src }: Props) {
  return (
    <video
      autoPlay
      loop
      muted
      playsInline
      preload="auto"
      className="absolute inset-0 h-full w-full object-cover"
      aria-hidden
    >
      <source src={src} type="video/mp4" />
    </video>
  );
}
