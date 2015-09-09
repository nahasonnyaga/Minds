/*
* Minds response object
*/
export interface MindsResponse {}

export interface MindsChannelResponse extends MindsResponse {
  status : string,
  message : string,
  channel : MindsUser
}

export interface MindsBlogResponse extends MindsResponse {
  blog : any
}

export interface MindsBlogListResponse extends MindsResponse {
  blogs : Array<any>,
  'load-next' : string
}

export interface MindsConversationResponse extends MindsResponse {
  conversations : Array<any>,
  'load-next' : string
}
