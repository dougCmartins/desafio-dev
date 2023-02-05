import axios, {AxiosInstance} from "axios";

const getHttpClient: AxiosInstance = axios.create({
    baseURL: 'http://127.0.0.1:8084/api/'
})

export default getHttpClient;