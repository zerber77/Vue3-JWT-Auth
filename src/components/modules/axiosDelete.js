import {ref} from 'vue'
import api from "./api";
import {QSpinnerGears, Loading} from "quasar";
import axios from "axios";
/// с параметрами все понятно вроде, добавлен showLoader для отображения спиннера загрузки, при false не отображать.
/// Не отображать, если элемент (например select имеет свой индикатор загрузки)
export function useDelete(url,dataToSend = {},options={},showLoader = true){
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
            const res = await api.delete(url,{ 
                                                data: dataToSend, 
                                                ...options 
                                            });
            response.value = res.data;
            if (showLoader) {
                Loading.hide();
            }
        } catch (e) {
            if (showLoader) {
                Loading.hide();
            }
            console.error('ERROR in useDelete:', e);
            throw e.message;
        }
    }
    return {response, request}
}
