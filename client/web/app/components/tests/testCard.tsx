import { Link } from "@remix-run/react";
import { useEffect, useState } from "react";
import deadlines from "~/routes/deadlines";

export const TestCard = (test: Test) => {
  const { title, exam_date, test_uuid, subject_uuid, subject } = test;
  const [readableDeadline, setReadableDeadline] = useState<string>("");
  const [isDanger, setIsDanger] = useState<boolean>(false);
  function parseDateForIndia(dateString: string): string {
    const parsedDate = new Date(dateString);

    if (isNaN(parsedDate.getTime())) {
      return "Invalid date";
    }

    const options: Intl.DateTimeFormatOptions = {
      year: "numeric",
      month: "long",
      day: "numeric",
      timeZone: "Asia/Kolkata",
    };

    const formattedDate = parsedDate.toLocaleString("en-IN", options);
    return formattedDate;
  }

  const calculateTimeUntilDeadline = (deadline: string) => {
    const now = new Date();
    const deadlineDate = new Date(deadline);
    const timeDifference = deadlineDate.getTime() - now.getTime();

    const daysUntilDeadline = Math.floor(
      timeDifference / (1000 * 60 * 60 * 24)
    );
    const hoursUntilDeadline = Math.floor(
      (timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
    );
    const minutesUntilDeadline = Math.floor(
      (timeDifference % (1000 * 60 * 60)) / (1000 * 60)
    );

    if (daysUntilDeadline > 0) {
      setIsDanger(false);
      setReadableDeadline(
        `${daysUntilDeadline} day${
          daysUntilDeadline === 1 ? "" : "s"
        } ${hoursUntilDeadline} hour${hoursUntilDeadline === 1 ? "" : "s"}`
      );
    } else if (hoursUntilDeadline > 0) {
      setIsDanger(true);
      setReadableDeadline(
        `${hoursUntilDeadline} hour${
          hoursUntilDeadline === 1 ? "" : "s"
        } ${minutesUntilDeadline} min${minutesUntilDeadline === 1 ? "" : "s"}`
      );
    } else {
      setIsDanger(true);
      setReadableDeadline(`Passed On ${formatDate(deadlineDate)}`);
    }
  };

  const formatDate = (date: Date): string => {
    const options: Intl.DateTimeFormatOptions = {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    };
    return date.toLocaleDateString("en-US", options);
  };

  useEffect(() => {
    if (exam_date != null) {
      calculateTimeUntilDeadline(exam_date);
    }
  }, [exam_date]);

  const isMobileViewport =
    typeof window !== "undefined" && window.innerWidth < 768;
  const truncatedTitle = title.length > 10 ? title.slice(0, 10) + "..." : title;

  return (
    <Link
      to={`/tests/${test_uuid}`}
      className="bg-mainLighter h-32 flex flex-col rounded-2xl hover:border-highlight border border-mainLighter duration-150 transition-all p-5"
    >
      <Link
        to={`/tests/${test_uuid}`}
        className="flex items-center justify-between w-full"
      >
        <h2 className="text-4xl font-base text-highlight">
          {!isMobileViewport ? title : truncatedTitle}
        </h2>
        <img src="/assets/examIcon.png" className="md:w-10 w-8" alt="" />
      </Link>

      {subject && subject_uuid && (
        <Link
          to={`/subjects/tests/${subject_uuid}`}
          className="text-highlightSecondary font-base md:text-base text-sm"
        >
          <div className="flex space-x-1">
            <p className="text-sm md:text-base">{subject}</p>
            <img src="/assets/book.svg" alt="" />
          </div>
        </Link>
      )}
      {exam_date != null && (
        <div
          className={`flex justify-between text-sm md:text-base space-x-0 md:justify-start md:space-x-2 ${
            !isDanger ? "text-highlightSecondary" : "text-[#B13232]"
          } font-base`}
        >
          <p>{readableDeadline}</p>
          <p>{parseDateForIndia(exam_date)}</p>
        </div>
      )}
    </Link>
  );
};
