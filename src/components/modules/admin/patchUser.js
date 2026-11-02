import {usePatch} from "../axiosPatch";

export async function patchUser(personData){
  const {response:data, request} = usePatch('api/admin/User/patch/', personData,{},true)
  try {
    await request()
  }catch (e) {
    console.log('ERROR in patchUser usePatch',e)
    throw e
  }
  //////здесь обрабатываем результат как вам надо
  console.log(data)
  return {data}
}
