import {useGet} from "../axiosGet";

export default async function getUsers(){
  const {response, request} = useGet('api/admin/User/getUsers/',{},true)
  try {
    await request()
  }catch (e) {
    console.log('ERROR in getUsers',e)
    throw e
  }
  return {response}
}
