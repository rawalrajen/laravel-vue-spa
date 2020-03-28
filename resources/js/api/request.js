import axios from 'axios'
import { getToken, setToken } from './auth'

// Create axios instance
const service = axios.create({
  baseURL: 'http://localhost:8000/api',
  timeout: 10000 // Request timeout
})

// Request intercepter
service.interceptors.request.use(
  config => {
    const token = getToken()
    if (token) {
      config.headers['Authorization'] = 'Bearer ' + getToken() // Set JWT token
    }

    return config
  },
  error => {
    // Do something with request error
    console.log(error) // for debug
    Promise.reject(error)
  }
)

// response pre-processing
service.interceptors.response.use(
  response => {
    if (response.headers.authorization) {
      setToken(response.headers.authorization)
      response.data.token = response.headers.authorization
    }

    return response.data
  },
  error => {
    let message = error.message
    if (error.response.data && error.response.data.errors) {
      message = error.response.data.errors
    } else if (error.response.data && error.response.data.error) {
      message = error.response.data.error
    }
    console.log(message)
    return Promise.reject(error)
  }
)

export default service
