import axios, {AxiosInstance} from "axios";

export interface ApiEnvelope<T> {
    data: T;
    message: string;
    code: string;
    status_code: number;
    errors: string[];
}

const getHttpClient: AxiosInstance = axios.create({
    baseURL: 'http://127.0.0.1:8084/api/'
})

export default getHttpClient;