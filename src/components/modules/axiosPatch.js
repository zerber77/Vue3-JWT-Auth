import {ref} from 'vue'
import api from "./api";
import {QSpinnerGears, Loading} from "quasar";
import axios from "axios";
/// с параметрами все понятно вроде, добавлен showLoader для отображения спиннера загрузки, при false не отображать.
/// Не отображать, если элемент (например select имеет свой индикатор загрузки)
export function usePatch(url,dataToSend = {},options={},showLoader = true){
    const response = ref()
    if (showLoader) Loading.show({
        spinner: QSpinnerGears,
        spinnerColor: 'blue-7',
        messageColor: 'black',
        backgroundColor: 'grey',
        message: 'Отправка данных'
    })
    const request = async () => {
        try {
            const res = await api.patch(url, dataToSend, options);
        //    const res = await api.post('http://quasar-test/'+url, dataToSend, options);
        //    const res = await axios.post('/'+url, dataToSend, options)  /////это серверный урл   spa/
            response.value = res.data;
            if (showLoader) {
                Loading.hide();
            }
        } catch (e) {
            if (showLoader) {
                Loading.hide();
            }
            console.error('ERROR in usePost:', e);
            throw e.message;
        }
    }
    return {response, request}
}
