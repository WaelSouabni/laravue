import request from '@/utils/request';
import axios from 'axios';

export function fetchList(query) {
  return request({
    url: '/packages',
    method: 'get',
    params: query,
  });
}
export function fetchPackage(id) {
  return request({
    url: '/packages/' + id,
    method: 'get',
  });
}
export function fetchPv(id) {
  return request({
    url: '/packages/' + id + '/pageviews',
    method: 'get',
  });
}
export function createPackage(data) {
  return request({
    url: '/packages',
    method: 'post',
    data,
  });
}
export function updatePackage(data) {
  return request({
    url: '/packages',
    method: 'put',
    data,
  });
}
export function deletePackage(id) {
  return request({
    url: '/packages/' + id,
    method: 'delete',
  });
}

export function masquerPackage(data) {
  return axios
    .get('http://127.0.0.1:8000/api/packages/masquer/' + data);
}

export function publierPackage(data) {
  return axios
    .get('http://127.0.0.1:8000/api/packages/publier/' + data);
}
