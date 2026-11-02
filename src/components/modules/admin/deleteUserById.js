import {useDelete} from "../axiosDelete";

export async function deleteUserById(personData){
    const {response:data, request} = useDelete('api/admin/User/deleteById/', personData,{},true)
    try {
        await request()
    }catch (e) {
        console.log('ERROR in patchUser deleteUserById',e)
        throw e
    }
    //////здесь обрабатываем результат как вам надо
    console.log(data)
    return {data}
}
