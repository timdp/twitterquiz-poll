#!/usr/bin/env node

const Twitter = require('twitter')
const fs = require('fs')
const twitterAuth = require('./config/auth/twitter.json')

const CONFIG_FILE = '../../config/teams.json'
const LIST_OWNER = 'Kroy_Wendy'

const client = new Twitter(twitterAuth)

const main = async () => {
  const lists = (await client.get('lists/list', {screen_name: LIST_OWNER}))
    .filter(({description}) => description.toLowerCase().includes('twitterquiz 2017'))
    .map(({id_str: id, name, description}) => {
      const [type, idxStr] = description.split(/\s/)
      const reserve = (type.toLowerCase() === 'reserve')
      return {
        listId: id,
        name: name + (reserve ? ' (reserve)' : ''),
        idx: parseInt(idxStr, 10) + (reserve ? 1000 : 0)
      }
    })
    .sort((a, b) => a.idx - b.idx)
  await Promise.all(lists.map(async (list) => {
    list.members = (await client.get('lists/members', {list_id: list.listId}))
      .users
      .map(({screen_name: name}) => name)
      .sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()))
  }))
  const options = lists.map(({name, members}) => ({name, members}))
  fs.writeFileSync(CONFIG_FILE, JSON.stringify(options, null, 2), 'utf8')
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})
