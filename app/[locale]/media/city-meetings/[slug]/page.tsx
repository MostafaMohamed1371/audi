import { createMediaDetailPage } from "@/lib/media-detail-page";

const { generateStaticParams, default: MediaCityMeetingsDetailPage } =
  createMediaDetailPage("cityMeetings");

export { generateStaticParams };
export default MediaCityMeetingsDetailPage;
