import localFont from "next/font/local";

export const rbFont = localFont({
  src: [
    {
      path: "./fonts/RB-Regular.ttf",
      weight: "400",
      style: "normal",
    },
    {
      path: "./fonts/RB-Bold.ttf",
      weight: "600",
      style: "normal",
    },
    {
      path: "./fonts/RB-Bold.ttf",
      weight: "700",
      style: "normal",
    },
  ],
  variable: "--font-rb",
  display: "swap",
});
