
import moment from 'moment';

export  const getDurationBetween = (date1 , date2 ) => {

    const parseDate1 = !date1 ? moment() : moment(date1);
    const parseDate2 = !date2 ? moment() : moment(date2);

    const differenceBetweenDates = parseDate1.diff( parseDate2);

    const duration = moment.duration( differenceBetweenDates ).as('hours');

    console.log({
        parseDate1,
        parseDate2,
        date1,
        date2,
        differenceBetweenDates,
        duration
    });

    return duration;

}
