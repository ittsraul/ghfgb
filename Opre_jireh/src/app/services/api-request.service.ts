import { Injectable } from '@angular/core';
import { Observable } from "rxjs";
import { HttpClient } from '@angular/common/http'

import { LoginResponse } from '../models/login-api';
import { Response } from '../models/events';
import { RegisterResponse } from '../models/register';
import { News } from '../models/news';
import { User } from '../models/user';
import { AssistResponse } from '../models/assist';


@Injectable({
  providedIn: 'root'
})
export class ApiRequestService {

  constructor(public http : HttpClient) { }

  login = "http://localhost:8000/api/login"
  events = "http://localhost:8000/api/events"
  register = "http://localhost:8000/api/insert/user"
  news = "http://localhost:8000/api/news"
  user = "http://localhost:8000/api/users/"
  update = "http://localhost:8000/api/update/users/"
  assist = "http://localhost:8000/api/update/event/"

  public getLoginResponse($email : string, $password : string) : Observable<LoginResponse> {
    return this.http.post<LoginResponse>(this.login, { "email" : $email, "password" : $password })
  }

  public getEventsResponse() : Observable<Response[]> {
    return this.http.get<Response[]>(this.events)
  }

  public getNews() : Observable<News[]> {
    return this.http.get<News[]>(this.news)
  }

  public registerResponse(email : string, name : string, surnames: string, password : string, phone : string) : Observable<RegisterResponse> {
    return this.http.post<RegisterResponse>(this.register, {
      "name" : name,
      "email" : email,
      "phone" : phone,
      "surnames" : surnames,
      "password" : password
    });
  }

  public getUser(id : number) : Observable<User> {
    return this.http.get<User>(this.user + id)
  }

  public updateUser(id : number, name : string, surnames : string, email : string, phone : string) : Observable<User> {
    return this.http.put<User>(this.update + id, {
      "name" : name,
      "surnames" : surnames,
      "email" : email,
      "phone" : phone
    })
  }

  public assistEventResponse(idUser : string | null, idEvent : number) : Observable<AssistResponse> {
    return this.http.put<AssistResponse>(this.assist + idEvent + "/" + idUser, { });
  }
}
