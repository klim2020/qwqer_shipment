import { fetchWorkingHours } from './transport'
import moment from 'moment';

console.log(moment.locale());

const isOpen = async () => {
   //console.log("fetching open hours");
    const data = await fetchWorkingHours();
   //console.log('recieved data is');
   //console.log(data);
    if (data){
       //console.log("we get openhours data");
       //console.log(data);
        const day = moment().locale("en").format("dddd");
       //console.log(day);
        const record = window.shipping_qwqer.workingHours.data.working_hours.filter((e)=> e.day_of_week === day);
        if (record.length > 0){
           //console.log(record);
            const format = 'hh:mm';
            window.time = moment();
            window.beforeTime = moment(record[0].time_from, format);
            window.afterTime = moment(record[0].time_to, format);
            const isOpen = window.beforeTime <  window.time &&  window.time <  window.afterTime;
           //console.log(isOpen);
            return isOpen;
        }
    }
    return false;
}

window.isOpen = isOpen;

export { isOpen }