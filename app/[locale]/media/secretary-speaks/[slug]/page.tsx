import { createMediaDetailPage } from "@/lib/media-detail-page";

const { generateStaticParams, default: MediaSecretarySpeaksDetailPage } =
  createMediaDetailPage("secretarySpeaks");

export { generateStaticParams };
export default MediaSecretarySpeaksDetailPage;
